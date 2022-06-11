//Hybrid MPI and OpenMP
#include <mpi.h>
#include <omp.h>
#include <stdio.h>
#include <time.h>
#include <unistd.h>

int main(int argc, char **argv)
{
    int rank, nprocs, namelen;
    char processor_name[MPI_MAX_PROCESSOR_NAME];
    int iam, np;

    MPI_Init(&argc, &argv);
    MPI_Comm_size(MPI_COMM_WORLD, &nprocs);
    MPI_Comm_rank(MPI_COMM_WORLD, &rank);
    MPI_Get_processor_name(processor_name, &namelen);

    omp_set_num_threads(3);

    #pragma omp parallel private(iam, np)
    {
        np = omp_get_num_threads();
        iam = omp_get_thread_num();
        int i, result;
        for (i = 0; i < 10; i++)
        {
            result += (int)rand() % 10;
            sleep(1);
        }
        printf("Hello from thread %d out of %d from process %d out of %d on %s computation result is %d\n", iam, np, rank, nprocs, processor_name, result);
    }

    MPI_Finalize();
}
